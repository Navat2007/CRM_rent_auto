import axios from "axios";
import buildFormData from "@utils/buildFormData.js";
import VueCookies from 'vue-cookies';

class YandexService {
    static async getIAM() {
        let form = new FormData();

        let result = null;
        const response = await axios.postForm(`${import.meta.env.VITE_SERVICE_API_URL}/getIAM.php`, form);

        console.log(response);

        if (response.data.params) {
            result = response.data.params.iamToken;
        }

        return result;
    }
}

export default YandexService