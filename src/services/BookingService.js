import axios from "axios";
import buildFormData from "@utils/buildFormData.js";

class BookingService {
    static directory = 'booking/rentalContracts';

    static async getAll(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = [];
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${this.directory}/load.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async getById(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = null;
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${this.directory}/load_by_id.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async add(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${this.directory}/add.php`, form);
    }

    static async edit(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${this.directory}/edit.php`, form);
    }

    static async archive(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${this.directory}/archive.php`, form);
    }

    static async delete(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${this.directory}/delete.php`, form);
    }
}

export default BookingService