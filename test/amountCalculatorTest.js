const calculateAmount = require('./amountCalculator');

describe('Amount Calculator', () => {
    test('returns 10000 for General Medicine', () => {
        expect(calculateAmount('General Medicine')).toBe(10000);
    });

    test('returns correct amount regardless of text case', () => {
        expect(calculateAmount('general medicine')).toBe(10000);
        expect(calculateAmount('GENERAL MEDICINE')).toBe(10000);
    });

    test('returns 20000 for other departments', () => {
        expect(calculateAmount('Orthopedics')).toBe(20000);
    });

    test('handles empty or null department names', () => {
        expect(calculateAmount('')).toBe(20000);
        expect(calculateAmount(null)).toBe(20000);
    });
});
