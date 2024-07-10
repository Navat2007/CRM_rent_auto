import axios from "axios";
import buildFormData from "@utils/buildFormData.js";

class UserService {
    static async getUsers(loading) {
        loading.value = true

        let result = [];
        const response = await axios.post(`${import.meta.env.VITE_API_URL}/admin/users/load.php`);

        if (response.data.params) {
            result = response.data.params;
        }

        loading.value = false

        return result;
    }

    static async addUser(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.post(`${import.meta.env.VITE_API_URL}/admin/users/add.php`, form);
    }
}

export default UserService