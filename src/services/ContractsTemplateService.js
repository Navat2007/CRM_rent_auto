import axios from "axios";
import buildFormData from "@utils/buildFormData.js";

class ContractsTemplateService {
    static directory = 'contracts_template';

    static async getBookingContractTemplate(company_id) {
        let form = new FormData();
        form.append('company_id', company_id);

        let result = null;
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${ContractsTemplateService.directory}/load_booking_contract_template.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async addBookingContractTemplate(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${ContractsTemplateService.directory}/add_booking_contract_template.php`, form);
    }

    static async deleteBookingContractTemplate(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/${ContractsTemplateService.directory}/delete_booking_contract_template.php`, form);
    }
}

export default ContractsTemplateService