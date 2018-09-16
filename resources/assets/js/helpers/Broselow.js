let colors = {
    gris: 4,
    rose: 6.5,
    rouge: 8.5,
    mauve: 10.5,
    jaune: 13,
    blanc: 16.5,
    bleu: 21,
    orange: 26.5,
    vert: 33
};

class Broselow {
    get(selectedColor) {
        return colors[selectedColor];
    }
}

export default new Broselow();