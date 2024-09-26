import {helpers} from "@vuelidate/validators";
import {computed} from "vue";
import moment from "moment/moment.js";

export const lettersAndDash = helpers.regex(/^[a-zA-Zа-яёА-ЯЁ-]*$/);

export const age = computed((state) => {
    const declOfNum = (number, words) => {
        return words[(number % 100 > 4 && number % 100 < 20) ? 2 : [2, 0, 1, 1, 1, 2][(number % 10 < 5) ? Math.abs(number) % 10 : 5]];
    }

    const today = new Date();
    const birthDate = moment(state.birthday, 'DD.MM.YYYY').toDate();
    let age = today.getFullYear() - birthDate.getFullYear();

    if (today.getMonth() < birthDate.getMonth() ||
        (today.getMonth() === birthDate.getMonth() && today.getDate() < birthDate.getDate())) {
        age--;
    }

    return state.birthday ? age + ' ' + declOfNum(age, ['год', 'года', 'лет']) : null;
});

export const zodiac = computed((state) => {
    function getZodiacSign(date) {
        const month = date.getMonth() + 1;
        const day = date.getDate();

        if (month == 1 && day >= 20 || month == 2 && day <= 18) return "Водолей";
        else if (month == 2 && day >= 19 || month == 3 && day <= 20) return "Рыбы";
        else if (month == 3 && day >= 21 || month == 4 && day <= 19) return "Овен";
        else if (month == 4 && day >= 20 || month == 5 && day <= 20) return "Телец";
        else if (month == 5 && day >= 21 || month == 6 && day <= 21) return "Близнецы";
        else if (month == 6 && day >= 22 || month == 7 && day <= 22) return "Рак";
        else if (month == 7 && day >= 23 || month == 8 && day <= 22) return "Лев";
        else if (month == 8 && day >= 23 || month == 9 && day <= 22) return "Дева";
        else if (month == 9 && day >= 23 || month == 10 && day <= 22) return "Весы";
        else if (month == 10 && day >= 23 || month == 11 && day <= 21) return "Скорпион";
        else if (month == 11 && day >= 22 || month == 12 && day <= 21) return "Стрелец";
        else if (month == 12 && day >= 22 || month == 1 && day <= 19) return "Козерог";
    }

    const birthDate = moment(state.birthday, 'DD.MM.YYYY').toDate();
    return state.birthday ? ' (' + getZodiacSign(birthDate) + ')' : null;
});