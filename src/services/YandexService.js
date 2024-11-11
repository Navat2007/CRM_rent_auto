import axios from "axios";
import buildFormData from "@utils/buildFormData.js";
import VueCookies from 'vue-cookies';

class YandexService {
    static async getIAM() {
        let result = VueCookies.get('iamToken');

        if(!result){
            let form = new FormData();
            const response = await axios.postForm(`${import.meta.env.VITE_SERVICE_API_URL}/yandex/getIAM.php`, form);

            if (response.data.params) {
                result = response.data.params.iamToken;
                VueCookies.set('iamToken', result, "10h");
            }
        }

        return result;
    }

    static async recognizePassport(base64) {
        let result = null;

        const mime = base64.split(';base64,')[0].split(':')[1];
        base64 = base64.split(',')[1];

        let form = new FormData();
        form.append("base64", base64);
        form.append("mime", mime);
        form.append("model", "passport");
        form.append("iamToken", VueCookies.get('iamToken'));
        form.append("folderID", import.meta.env.VITE_YANDEX_FOLDER_ID);

        const response = await axios.postForm(`${import.meta.env.VITE_SERVICE_API_URL}/yandex/recognizeText.php`, form);

        // console.log(response.data);

        if (response.data.params) {
            result = response.data.params.result.textAnnotation;
        }

        return result;
    }

    static async recognizeDriverLicense(base64) {
        let result = null;

        const mime = base64.split(';base64,')[0].split(':')[1];
        base64 = base64.split(',')[1];

        let form = new FormData();
        form.append("base64", base64);
        form.append("mime", mime);
        form.append("model", "driver-license-front");
        form.append("iamToken", VueCookies.get('iamToken'));
        form.append("folderID", import.meta.env.VITE_YANDEX_FOLDER_ID);

        const response = await axios.postForm(`${import.meta.env.VITE_SERVICE_API_URL}/yandex/recognizeText.php`, form);

        // console.log(response.data);

        if (response.data.params) {
            result = response.data.params.result.textAnnotation;
        }

        return result;
    }
}

export default YandexService