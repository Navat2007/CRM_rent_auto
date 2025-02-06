<script setup>
import {computed, reactive, unref} from "vue";
import Divider from "primevue/divider";
import {useAuthStore} from "@stores";
import {helpers, required} from "@vuelidate/validators";
import {useVuelidate} from "@vuelidate/core";
import FormError from "@components/Inputs/FormError.vue";

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
    item: {
        type: Object,
        required: true
    }
})
const emit = defineEmits(['onSubmit', 'onDelete']);

const state = reactive({
    companyId: user.company_id,
    days_from: props.item.days_from ? parseInt(props.item.days_from) : null,
    days_until: props.item.days_until ? parseInt(props.item.days_until) : null,
    active: props.item.active,
});
const rules = computed(() => {
    return {
        days_from: {
            required: helpers.withMessage("Нужно заполнить 'Дней от'", required),
            minValue: helpers.withMessage("'Дней от' должно быть меньше либо равно 'Дней до'", value => {
                if(state.days_until && state.days_from > state.days_until) {
                    return false;
                }

                return true;
            }),
            $lazy: true
        },
        days_until: {
            $lazy: true
        },
    }
});
const v$ = useVuelidate(rules, state);

const onFormSubmit = async (e) => {
    const isFormCorrect = await unref(v$).$validate();

    if (isFormCorrect) {
        emit('onSubmit', state);
    }
};
</script>

<template>
    <Card class="w-full lg:w-2/3">
        <template #title>{{ title }}</template>
        <template #content>
            <form @submit.prevent="onFormSubmit">
                <div class="grid gap-4 mb-4 mt-2 sm:grid-cols-1">
                    <!-- Дней от -->
                    <div>
                        <FloatLabel variant="on">
                            <InputNumber id="days_from" min="0" v-model="state.days_from" fluid/>
                            <label for="days_from">Дней от</label>
                        </FloatLabel>
                    </div>
                    <!-- Дней до -->
                    <div>
                        <FloatLabel variant="on">
                            <InputNumber id="days_until" min="0" v-model="state.days_until" fluid/>
                            <label for="days_until">Дней до</label>
                        </FloatLabel>
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
                <Divider v-if="user.access.directory === 2" type="dashed"/>
                <p v-for="error of v$.$errors" :key="error.$uid" class="text-red-500 mb-4">
                    {{ error.$message }}
                </p>
                <Button v-if="user.access.directory === 2" type="submit" icon="pi pi-save" label="Сохранить" outlined/>
                <Button class="ml-4" v-if="user.id === 1" @click="emit('onDelete')" icon="pi pi-trash" label="Удалить"
                        outlined severity="danger"/>
            </form>
        </template>
    </Card>
</template>