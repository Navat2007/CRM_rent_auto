import axios from "axios";
import buildFormData from "@utils/buildFormData.js";

class UserService {
    static async getCompanies(loading) {
        loading.value = true

        let result = [];
        const response = await axios.post(`${import.meta.env.VITE_API_URL}/admin/companies/load.php`);

        if (response.data.params) {
            result = response.data.params;
        }

        loading.value = false

        return result;
    }

    static async addCompany(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.post(`${import.meta.env.VITE_API_URL}/admin/companies/add.php`, form);
    }
}

export default UserService