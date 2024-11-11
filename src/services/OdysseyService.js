import axios from "axios";
import moment from "moment";

class OdysseyService {
    static async sendRequest(f, i, o, birthday) {
        let result = null;

        let form = new FormData();
        form.append("f", f);
        form.append("i", i);
        form.append("o", o);
        form.append("birthday", birthday);

        const response = await axios.postForm(`${import.meta.env.VITE_SERVICE_API_URL}/odyssey/check.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }
    static async saveResult(result) {
        let form = new FormData();
        form.append("user_id", result.user_id);
        form.append("scoring", result.scoring);
        form.append("url", result.url);
        form.append("f", result.f);
        form.append("i", result.i);
        form.append("o", result.o);
        form.append("birthday", moment(result.birthday, "DD.MM.YYYY").format("YYYY-MM-DD"));

        const response = await axios.postForm(`${import.meta.env.VITE_SERVICE_API_URL}/odyssey/save.php`, form);
    }
    static async getResults(user_id) {
        let form = new FormData();
        form.append("user_id", user_id);

        let result = null;

        const response = await axios.postForm(`${import.meta.env.VITE_SERVICE_API_URL}/odyssey/load.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }
}

export default OdysseyService