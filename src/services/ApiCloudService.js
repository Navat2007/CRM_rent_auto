import axios from "axios";
import buildFormData from "@utils/buildFormData.js";

class ApiCloudService {
    static async sendRequest(type, data) {
        let result = null;
        let method = null;

        let form = new FormData();
        buildFormData(form, data);

        switch (type){
            case 'gibdd_driver':
                method = 'driver';
                break;
            case 'rsa_kbm':
                method = 'kbm';
                break;
            case 'fssp_physical':
                method = 'physical';
                break;
            case 'nalog_inn':
                method = 'inn';
                break;
            case 'mvd_chekpassport':
                method = 'chekpassport';
                break;
            case 'bankrot_searchstring':
                method = 'searchString';
                break;
        }

        if(!method)
            return null;

        const response = await axios.postForm(`${import.meta.env.VITE_SERVICE_API_URL}/api-cloud/${method}.php`, form);

        console.log(response);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }
    static async getResults(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = null;

        const response = await axios.postForm(`${import.meta.env.VITE_SERVICE_API_URL}/api-cloud/load.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }
}

export default ApiCloudService