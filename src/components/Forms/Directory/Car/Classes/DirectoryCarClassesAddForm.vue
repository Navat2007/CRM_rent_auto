<script setup>
import Divider from "primevue/divider";
import {useAuthStore} from "@stores";
import {computed, onMounted, reactive, ref, unref} from "vue";
import {helpers, required} from "@vuelidate/validators";
import {useVuelidate} from "@vuelidate/core";
import FormError from "@components/Inputs/FormError.vue";
import DirectoryService from "@services/DirectoryService.js";

const {user} = useAuthStore();

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    sending: {
        type: Boolean,
        required: false,
        default: false
    },
    card: {
        type: Boolean,
        required: false,
        default: true
    },
})
const emit = defineEmits(['onSubmit']);

const state = reactive({
    companyId: user.company_id,
    name: "",
    limit: 0,
    deposit: 0,
    cost_extra_mileage: 0,
    active: true
});
const rules = computed(() => {
    return {
        name: {
            required: helpers.withMessage("Название должно быть заполнено", required),
            $lazy: true
        },
        limit: {
            $lazy: true
        },
    }
});
const v$ = useVuelidate(rules, state);

const loadingCarClassServices = ref(true);
const isCarClassServicesDrawerOpen = ref(false);
const carClassServices = ref([]);

const onFormSubmit = async (e) => {
    const isFormCorrect = await unref(v$).$validate();

    if (isFormCorrect) {
        emit('onSubmit', state);
    }
};

async function fetchCarClassServices() {
    carClassServices.value = (await DirectoryService.getAll({directory: 'directory_services', company_id: user.company_id}))
        .filter(person => person.archive === "Активен");
    loadingCarClassServices.value = false;

    state.class_services = carClassServices.value.map((item) => {
        return {
            id: item.id,
            name: item.name,
            price: null
        }
    });
}

onMounted(() => {
    fetchCarClassServices();
});
</script>

<template>
    <Card v-if="card" class="w-full lg:w-2/3">
        <template #title>{{ title }}</template>
        <template #content>
            <form @submit.prevent="onFormSubmit">
                <div class="grid gap-8 mb-4 mt-6 sm:grid-cols-1">
                    <!-- Название -->
                    <div>
                        <FloatLabel variant="over">
                            <InputText id="name" v-model="state.name" fluid/>
                            <label for="name">Название*</label>
                        </FloatLabel>
                        <FormError :errors="v$.name.$errors"/>
                    </div>
                    <div class="grid gap-8 md:gap-2 grid-cols-1 sm:grid-cols-3">
                        <!-- Лимит пробега в сутки -->
                        <div>
                            <FloatLabel variant="over">
                                <InputNumber id="limit" min="0" v-model="state.limit" fluid/>
                                <label for="limit">Лимит пробега в сутки</label>
                            </FloatLabel>
                            <FormError :errors="v$.limit.$errors"/>
                        </div>
                        <!-- Стоимость 1 км перепробега -->
                        <div>
                            <FloatLabel variant="over">
                                <InputNumber id="cost_extra_mileage" min="0" v-model="state.cost_extra_mileage" fluid/>
                                <label for="cost_extra_mileage">Стоимость 1 км перепробега (руб.)</label>
                            </FloatLabel>
                        </div>
                        <!-- Депозит -->
                        <div>
                            <FloatLabel variant="over">
                                <InputNumber id="deposit" min="0" v-model="state.deposit" fluid/>
                                <label for="deposit">Залог (руб.)</label>
                            </FloatLabel>
                        </div>
                    </div>

                    <Panel header="Стоимость услуг" toggleable collapsed>
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs bg-gray-200 text-gray-700 uppercase  dark:text-gray-200">
                            <tr>
                                <th scope="col" class="py-3 px-2">Услуга</th>
                                <th scope="col" class="py-3 px-2">Стоимость</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                                v-for="(item, index) in state.class_services" :key="item"
                                class="bg-white dark:bg-gray-800 dark:border-gray-700 border-gray-200"
                                :class="{'border-b': index !== state.class_services.length - 1}"
                            >
                                <th scope="row" class="py-4 px-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ item.name }}</th>
                                <td><InputNumber v-model="item.price" min="0" /></td>
                            </tr>
                            </tbody>
                        </table>
                    </Panel>

                    <!-- Активен? -->
                    <div class="flex items-center">
                        <input id="active" type="checkbox"
                               v-model="state.active"
                               class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
                        <label for="active" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                            Активен?
                        </label>
                    </div>
                </div>
                <Divider type="dashed"/>
                <Button v-if="user.access.directory === 2" type="submit" icon="pi pi-plus" label="Добавить" outlined/>
            </form>
        </template>
    </Card>
    <div v-else>
        <form @submit.prevent="onFormSubmit">
            <div class="grid gap-8 mb-4 mt-6 sm:grid-cols-1">
                <!-- Название -->
                <div>
                    <label for="name"
                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Название*</label>
                    <input
                        v-model="state.name"
                        type="text" id="name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="..."
                    >
                    <FormError :errors="v$.name.$errors"/>
                </div>
                <div class="grid gap-8 md:gap-4 grid-cols-1 sm:grid-cols-3">
                    <!-- Лимит пробега в сутки -->
                    <div class="flex flex-col justify-end">
                        <label for="limit"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Лимит пробега в сутки</label>
                        <InputNumber id="limit" min="0" v-model="state.limit" fluid/>
                    </div>
                    <!-- Стоимость 1 км перепробега -->
                    <div class="flex flex-col justify-end">
                        <label for="cost_extra_mileage"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Стоимость 1 км перепробега</label>
                        <InputNumber id="cost_extra_mileage" min="0" v-model="state.cost_extra_mileage" fluid/>
                    </div>
                    <!-- Депозит -->
                    <div class="flex flex-col justify-end">
                        <label for="deposit"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Залог (руб.)</label>
                        <InputNumber id="deposit" min="0" v-model="state.deposit" fluid/>
                    </div>
                </div>
                <!-- Активен? -->
                <div class="flex items-center">
                    <input id="active" type="checkbox"
                           v-model="state.active"
                           class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
                    <label for="active" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        Активен?
                    </label>
                </div>
            </div>
            <Divider type="dashed"/>
            <Button v-if="user.access.directory === 2" type="submit" icon="pi pi-plus" label="Добавить" outlined/>
        </form>
    </div>
</template>