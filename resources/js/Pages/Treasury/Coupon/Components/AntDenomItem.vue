<template>
    <a-form ref="formRef" name="dynamic_form_nest_item" :model="dynamicValidateForm" @finish="onFinish">
        <a-space v-for="(denom, index) in dynamicValidateForm.denom" :key="index"
            style="display: flex; margin-bottom: 8px" align="baseline">
            <a-form-item :name="['denom', index, 'denomination']" :rules="{
                required: true,
                message: 'Missing Denomination',
            }">
                <a-input v-model:value="denom.denomination" placeholder="First Name" />
            </a-form-item>
            <a-form-item :name="['denom', index, 'quantity']" :rules="{
                required: true,
                message: 'Missing Quantity',
            }">
                <a-input v-model:value="denom.quantity" placeholder="Last Name" />
            </a-form-item>
            <MinusCircleOutlined @click="removeDenom(denom)" />
        </a-space>

        <div class="flex justify-between mt-2">
            <a-button @click="addDenom" type="dashed" class="mr-2">
                <PlusOutlined />
                Add Denomination
            </a-button>
            <a-button block type="primary" html-type="submit">Submit</a-button>
        </div>
    </a-form>
</template>
<script setup>
import { reactive, ref } from 'vue';

const props = defineProps({
    form: Array,
});

const formRef = ref();

const dynamicValidateForm = reactive({
    denom: props.form,
});

const removeDenom = item => {
    const index = dynamicValidateForm.denom.indexOf(item);
    if (index !== -1) {
        dynamicValidateForm.denom.splice(index, 1);
    }
};

const addDenom = () => {
    dynamicValidateForm.denom.push({
        denomination: '',
        quantity: '',
    });
};
const emit = defineEmits(['submit-form']);

const onFinish = values => {
    emit('submit-form');

};

</script>
