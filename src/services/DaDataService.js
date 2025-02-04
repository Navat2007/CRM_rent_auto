import axios from "axios";
import buildFormData from "@utils/buildFormData.js";

class DaDataService {
    static async GetAddress(data) {
        let result = null;
        let requestData = {
            address_string: data
        }

        let form = new FormData();
        buildFormData(form, requestData);

        const response = await axios.postForm(`${import.meta.env.VITE_SERVICE_API_URL}/dadata/get_address.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }
}

export default DaDataService