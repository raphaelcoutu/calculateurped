let masculin = {
    mois_1:4.5,
    mois_2:5.5,
    mois_3:6.5,
    mois_4:7,
    mois_5:7.5,
    mois_6:8,
    mois_7:8,
    mois_8:8.5,
    mois_9:9,
    mois_10:9,
    mois_11:9.5,
    mois_12:10,
    mois_13:10,
    mois_14:10,
    mois_15:10.5,
    mois_16:10.5,
    mois_17:11,
    mois_18:11,
    mois_19:11,
    mois_20:11.5,
    mois_21:12,
    mois_22:12,
    mois_23:12,
    mois_24:12,
    ans_3:14,
    ans_4:16,
    ans_5:18,
    ans_6:20,
    ans_7:24,
    ans_8:25,
    ans_9:28,
    ans_10:31,
    ans_11:35,
    ans_12:39,
    ans_13:44,
    ans_14:51,
    ans_15:57,
    ans_16:63,
    ans_17:65,
    ans_18:68,
};
let feminin = {
    mois_1:4,
    mois_2:5,
    mois_3:6,
    mois_4:6.5,
    mois_5:7,
    mois_6:7,
    mois_7:7.5,
    mois_8:8,
    mois_9:8,
    mois_10:8.5,
    mois_11:9,
    mois_12:9,
    mois_13:9,
    mois_14:9.5,
    mois_15:9.5,
    mois_16:10,
    mois_17:10,
    mois_18:10,
    mois_19:10.5,
    mois_20:10.5,
    mois_21:11,
    mois_22:11,
    mois_23:11,
    mois_24:11.5,
    ans_3:14,
    ans_4:16,
    ans_5:18,
    ans_6:20,
    ans_7:22,
    ans_8:25,
    ans_9:28,
    ans_10:32,
    ans_11:36,
    ans_12:42,
    ans_13:48,
    ans_14:51,
    ans_15:53,
    ans_16:55,
    ans_17:55,
    ans_18:56,
};


class AgeSexe {
    get(age, sexe) {
        if (sexe === "m") {
            return masculin[age];
        } else if (sexe === "f") {
            return feminin[age]
        }

        return null;
    }
}

export default new AgeSexe();