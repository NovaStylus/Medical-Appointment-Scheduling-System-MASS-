function calculateAmount(departmentName) {
    if (departmentName && departmentName.toLowerCase().includes('general medicine')) {
        return 10000;
    } else {
        return 20000;
    }
}

module.exports = calculateAmount;
