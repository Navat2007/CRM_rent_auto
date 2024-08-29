import axios from "axios";
import buildFormData from "@utils/buildFormData.js";

class UserService {
    static async getUsers(company_id) {
        let form = new FormData();
        form.append('company_id', company_id);

        let result = [];
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/users/load.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async addUser(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/users/add.php`, form);
    }

    static async editUser(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/users/edit.php`, form);
    }
}

export default UserService