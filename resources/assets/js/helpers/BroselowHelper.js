let colors = {
    grey: 4,
    pink: 6.5,
    red: 8.5,
    purple: 10.5,
    yellow: 13,
    white: 16.5,
    blue: 21,
    orange: 26.5,
    green: 33
};

class BroselowHelper {
    get(selectedColor) {
        return colors[selectedColor];
    }
}

export default new BroselowHelper();