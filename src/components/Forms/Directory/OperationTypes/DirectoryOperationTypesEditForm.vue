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
    name: props.item.name,
    order: parseInt(props.item.order),
    is_income: parseInt(props.item.is_income) === 1 ? "true" : "false",
    used_for: "none",
    active: props.item.active,
});
const rules = computed(() => {
    return {
        name: {
            required: helpers.withMessage("Название должно быть заполнено", required),
            $lazy: true
        },
        order: {
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

console.log(props.item);
</script>

<template>
    <Card class="w-full lg:w-2/3">
        <template #title>{{ title }}</template>
        <template #content>
            <form @submit.prevent="onFormSubmit">
                <div class="grid gap-4 mb-4 mt-2 sm:grid-cols-1">
                    <!-- Название -->
                    <div>
                        <FloatLabel variant="on">
                            <InputText id="name" v-model="state.name" fluid/>
                            <label for="name">Название*</label>
                        </FloatLabel>
                        <FormError :errors="v$.name.$errors"/>
                    </div>
                    <!-- Порядок отображения -->
                    <div>
                        <FloatLabel variant="on">
                            <InputNumber id="order" min="0" v-model="state.order" fluid/>
                            <label for="order">Порядок отображения</label>
                        </FloatLabel>
                        <FormError :errors="v$.order.$errors"/>
                    </div>
                    <!-- Доходная или расходная -->
                    <Fieldset legend="Доходная или расходная операция">
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center gap-2">
                                <RadioButton v-model="state.is_income" inputId="is_income1" name="income" value="true"/>
                                <label for="is_income1">Доход</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <RadioButton v-model="state.is_income" inputId="is_income2" name="expence"
                                             value="false"/>
                                <label for="is_income2">Расход</label>
                            </div>
                        </div>
                    </Fieldset>
                    <!-- Используется для -->
                    <Fieldset legend="Используется для">
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center gap-2">
                                <RadioButton v-model="state.used_for" inputId="used_for1" name="none" value="none"/>
                                <label for="used_for1">Не используется</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <RadioButton v-model="state.used_for" inputId="used_for2" name="pay_rent" value="pay_rent"/>
                                <label for="used_for2">Оплата аренды</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <RadioButton v-model="state.used_for" inputId="used_for3" name="pay_deposit" value="pay_deposit"/>
                                <label for="used_for3">Оплата залога</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <RadioButton v-model="state.used_for" inputId="used_for4" name="return_deposit"
                                             value="return_deposit"/>
                                <label for="used_for4">Возврат залога</label>
                            </div>
                        </div>
                    </Fieldset>
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
                <Button v-if="user.access.directory === 2" type="submit" icon="pi pi-save" label="Сохранить" outlined/>
                <Button class="ml-4" v-if="user.id === 1" @click="emit('onDelete')" icon="pi pi-trash" label="Удалить"
                        outlined severity="danger"/>
            </form>
        </template>
    </Card>
</template>