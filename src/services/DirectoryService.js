import axios from "axios";
import buildFormData from "@utils/buildFormData.js";

class DirectoryService {
    static async getPositions(company_id) {
        let form = new FormData();
        form.append('company_id', company_id);

        let result = [];
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/position/load.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async getPositionsById(id) {
        let form = new FormData();
        form.append('id', id);

        let result = null;
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/position/load_by_id.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async addPositions(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/position/add.php`, form);
    }

    static async editPositions(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/position/edit.php`, form);
    }

    static async getAdvertisingTypes(company_id) {
        let form = new FormData();
        form.append('company_id', company_id);

        let result = [];
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/advertising_types/load.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async getAdvertisingTypesById(id) {
        let form = new FormData();
        form.append('id', id);

        let result = null;
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/advertising_types/load_by_id.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async addAdvertisingTypes(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/advertising_types/add.php`, form);
    }

    static async editAdvertisingTypes(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/advertising_types/edit.php`, form);
    }
}

export default DirectoryService