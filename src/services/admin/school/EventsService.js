import axios from "axios";
import buildFormData from "@utils/buildFormData.js";

class EventsService {
    static directory = "admin/school/events";

    static async getAll(loading) {
        loading.value = true

        let result = [];
        const response = await axios.post(`${import.meta.env.VITE_API_URL}/${EventsService.directory}/load.php`);

        if (response.data.params) {
            result = response.data.params;
        }

        loading.value = false

        return result;
    }

    static async add(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.post(`${import.meta.env.VITE_API_URL}/${EventsService.directory}/add.php`, form);
    }
}

export default EventsService