const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '..', 'index.php'), 'utf8');
function section(start, end) {
    const from = source.indexOf(start);
    const to = source.indexOf(end, from);
    assert.ok(from >= 0 && to > from, `Missing source section: ${start}`);
    return source.slice(from, to);
}
const parser = section('const exp=', '\nconst toast=') + '\n' +
    section('function subscriptionTimestamp(', '\nfunction remainingNotice(');
const usage = section('let UQ=0,UC;', "\ndocument.addEventListener('click'");
const forecast = section('async function fcLoad()', '\nfunction fcView()');

function harness(now = new Date('2026-01-08T00:00:00Z'), range = '24H', rows = []) {
    const requests = [];
    const stats = {dataset: {}, hidden: false};
    class FixedDate extends Date {
        constructor(...args) { super(...(args.length ? args : [now.getTime()])); }
        static now() { return now.getTime(); }
    }
    const context = vm.createContext({
        Date: FixedDate, AbortController, URLSearchParams, setTimeout,
        D: {data_limit: 10000, used_traffic: 1}, R: range, P: '/sub/test',
        U: [], PD: 'hour', FC: null, OFF: 0,
        $: () => stats, draw() {}, toast() {}, t: key => key, fcView() {},
        jget: async url => {
            requests.push(new URL(url, 'https://subscription.example'));
            return {stats: {'-1': rows}};
        },
    });
    vm.runInContext(parser + '\n' + usage + '\n' + forecast +
        '\nglobalThis.parseExpiry = exp;', context);
    return {context, requests};
}

test('expiry and reminder accept timestamps as numbers and numeric strings', () => {
    const {context} = harness();
    for (const value of [1798761600, '1798761600', 1798761600000, '1798761600000', ' 1798761600 ']) {
        assert.equal(context.parseExpiry(value), 1798761600000, `dashboard: ${value}`);
        assert.equal(context.subscriptionTimestamp(value), 1798761600000, `reminder: ${value}`);
    }
});

test('timezone-less panel timestamps are UTC and explicit offsets are preserved', () => {
    const {context} = harness();
    for (const value of ['2027-01-01T00:00:00', '2027-01-01T00:00:00Z', '2027-01-01T03:30:00+03:30', '2027-01-01']) {
        assert.equal(context.parseExpiry(value), Date.parse('2027-01-01T00:00:00Z'));
        assert.equal(context.subscriptionTimestamp(value), context.parseExpiry(value));
    }
    assert.equal(context.parseExpiry('2027-01-01T00:00:00.123'), Date.parse('2027-01-01T00:00:00.123Z'));
});

test('missing, unlimited and invalid timestamps have a safe zero value', () => {
    const {context} = harness();
    for (const value of [null, undefined, '', ' ', 0, '0', -1, '-1', NaN, Infinity, 'invalid', {}, true, 1e20]) {
        assert.equal(context.parseExpiry(value), 0, `dashboard: ${String(value)}`);
        assert.equal(context.subscriptionTimestamp(value), 0, `reminder: ${String(value)}`);
    }
});

for (const [year, month, day] of [[2026, 0, 31], [2025, 0, 31], [2026, 2, 31], [2026, 10, 30], [2024, 1, 29], [2026, 11, 31]]) {
    test(`12-month chart includes all months on ${year}-${month + 1}-${day}`, async () => {
        const now = new Date(year, month, day, 12);
        const {context, requests} = harness(now, '12M');
        await context.usage();
        assert.equal(requests.length, 1);
        assert.equal(requests[0].searchParams.get('period'), 'month');
        assert.equal(requests[0].searchParams.get('start'), new Date(year, month - 11, 1).toISOString());
        assert.equal(requests[0].searchParams.get('end'), now.toISOString());
    });
}

test('24-hour, daily and all-time chart ranges retain their boundaries', async () => {
    const now = new Date(2026, 2, 9, 12);
    for (const [range, period, start] of [
        ['24H', 'hour', new Date(now.getTime() - 86400000)],
        ['7D', 'day', new Date(2026, 2, 3)],
        ['30D', 'day', new Date(2026, 1, 8)],
        ['ALL', 'month', new Date(0)],
    ]) {
        const {context, requests} = harness(now, range);
        await context.usage();
        assert.equal(requests[0].searchParams.get('period'), period, range);
        assert.equal(requests[0].searchParams.get('start'), start.toISOString(), range);
        assert.equal(requests[0].searchParams.get('end'), now.toISOString(), range);
    }
});

test('usage buckets use UTC panel dates and discard invalid timestamps', async () => {
    const {context} = harness(undefined, '24H', [
        {period_start: '2026-01-07T00:00:00', total_traffic: 100},
        {period_start: '2026-01-07T03:30:00+03:30', total_traffic: 200},
        {period_start: 'invalid', total_traffic: 999},
        {period_start: null, total_traffic: 999},
    ]);
    await context.usage();
    assert.equal(context.U.length, 2);
    for (const bucket of context.U) assert.equal(bucket.d.getTime(), Date.parse('2026-01-07T00:00:00Z'));
});

test('consumption forecast uses elapsed UTC time and ignores invalid buckets', async () => {
    const {context} = harness(undefined, '7D', [
        {period_start: 'invalid', total_traffic: 999},
        {period_start: '2026-01-02T00:00:00', total_traffic: 600},
    ]);
    await context.fcLoad();
    assert.equal(context.FC.r, 100);
});
