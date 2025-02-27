import axios from "axios";
import buildFormData from "@utils/buildFormData.js";

class DirectoryService {
    // Positions
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

    static async deletePositions(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/position/delete.php`, form);
    }

    // AdvertisingTypes
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

    static async deleteAdvertisingTypes(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/advertising_types/delete.php`, form);
    }

    // Services
    static async addService(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/services/add.php`, form);
    }

    static async editService(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/services/edit.php`, form);
    }

    // Operation Types
    static async addOperationType(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/operation_types/add.php`, form);
    }

    static async editOperationType(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/operation_types/edit.php`, form);
    }

    // Payment Types
    static async addPaymentType(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/payment_types/add.php`, form);
    }

    static async editPaymentType(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/payment_types/edit.php`, form);
    }

    // Car classes
    static async addCarClass(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/classes/add.php`, form);
    }

    static async editCarClass(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/classes/edit.php`, form);
    }

    // Car classes service price
    static async addCarClassServicePrice(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/classes_service_price/add.php`, form);
    }

    static async editCarClassServicePrice(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/classes_service_price/edit.php`, form);
    }

    static async getAllCarClassServicePrices(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = [];
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/classes_service_price/load.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async getCarClassServicePriceById(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = null;
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/classes_service_price/load_by_id.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async getCarClassServicePriceByClassId(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = null;
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/classes_service_price/load_for_class.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    // Car models
    static async addCarModel(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/models/add.php`, form);
    }

    static async editCarModel(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/models/edit.php`, form);
    }

    static async getAllModels(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = [];
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/models/load.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async getModelById(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = null;
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/models/load_by_id.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    // Car generations
    static async addCarGeneration(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/generations/add.php`, form);
    }

    static async editCarGeneration(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/generations/edit.php`, form);
    }

    static async getAllGenerations(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = [];
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/generations/load.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async getGenerationById(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = null;
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/generations/load_by_id.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    // Car configurations
    static async addCarConfiguration(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/configurations/add.php`, form);
    }

    static async editCarConfiguration(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/configurations/edit.php`, form);
    }

    static async getAllConfigurations(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = [];
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/configurations/load.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async getConfigurationById(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = null;
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/car/configurations/load_by_id.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    // Price periods
    static async getPricePeriods(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = [];
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/price_periods/load.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async getPricePeriodById(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = null;
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/price_periods/load_by_id.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async addPricePeriod(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/price_periods/add.php`, form);
    }

    static async editPricePeriod(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/price_periods/edit.php`, form);
    }

    static async deletePricePeriod(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/price_periods/delete.php`, form);
    }

    // Universal
    static async getAll(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = [];
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/universal/load.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async getById(data) {
        let form = new FormData();
        buildFormData(form, data);

        let result = null;
        const response = await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/universal/load_by_id.php`, form);

        if (response.data.params) {
            result = response.data.params;
        }

        return result;
    }

    static async add(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/universal/add.php`, form);
    }

    static async edit(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/universal/edit.php`, form);
    }

    static async delete(data) {
        let form = new FormData();
        buildFormData(form, data);

        return await axios.postForm(`${import.meta.env.VITE_API_URL}/admin/directory/universal/delete.php`, form);
    }
}

export default DirectoryService