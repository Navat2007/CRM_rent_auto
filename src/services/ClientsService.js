import axios from "axios";
import buildFormData from "@utils/buildFormData.js";

class ClientsService {
    static directory = 'clients';

    static async getClients(company_id) {
        let form = new FormData();
        form.append('company_id', company_id);

        let result = [];
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${ClientsService.directory}/load.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async getAllForBooking(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = [];
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${this.directory}/load_for_booking.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async getClientById(id) {
        let form = new FormData();
        form.append('id', id);

        let result = null;
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${ClientsService.directory}/load_by_id.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async addClient(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${ClientsService.directory}/add.php`, form);
    }

    static async editClient(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${ClientsService.directory}/edit.php`, form);
    }

    static async archiveClient(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${ClientsService.directory}/archive.php`, form);
    }

    static async deleteClient(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${ClientsService.directory}/delete.php`, form);
    }
}

export default ClientsService