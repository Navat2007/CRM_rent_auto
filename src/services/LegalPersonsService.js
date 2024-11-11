import axios from "axios";
import buildFormData from "@utils/buildFormData.js";

class LegalPersonsService {
    static async getLegalPersons(company_id) {
        let form = new FormData();
        form.append('company_id', company_id);

        let result = [];
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/legal_persons/load.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async getLegalPersonById(id) {
        let form = new FormData();
        form.append('id', id);

        let result = null;
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/legal_persons/load_by_id.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async addLegalPerson(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/legal_persons/add.php`, form);
    }

    static async editLegalPerson(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/legal_persons/edit.php`, form);
    }

    static async archiveLegalPerson(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/legal_persons/archive.php`, form);
    }

    static async deleteLegalPerson(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/legal_persons/delete.php`, form);
    }
}

export default LegalPersonsService