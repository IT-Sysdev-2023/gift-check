<template>
    <a-drawer class="custom-class" root-class-name="root-class-name" title="Scan Barcode Drawer" placement="right">
        <div class="flex justify-center">
            <a-typography-text keyboard>Scan Barcode:</a-typography-text>
        </div>
        <a-input-number v-model:value="form.barcode" allow-clear style="width: 100%" @keyup.enter="validate"
            size="large" @change="barcodeHandler" />
        <a-divider>Details</a-divider>
        <a-descriptions class="mb-1" size="small" layout="horizontal" bordered>
            <a-descriptions-item style="width: 50%;" label="Receiving No.">{{ rec }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions class="mb-1" size="small" layout="horizontal" bordered>
            <a-descriptions-item style="width: 50%;" label="Store.">{{ record.store.store_name }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions class="mb-1" size="small" layout="horizontal" bordered>
            <a-descriptions-item style="width: 50%;" label="Date.">{{ dayjs().format('MMM, DD YYYY')
                }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions class="mb-1" size="small" layout="horizontal" bordered>
            <a-descriptions-item style="width: 50%;" label="Denomination">₱ {{
                record.gc.denomination.denomination.toLocaleString() }}</a-descriptions-item>
        </a-descriptions>
        {{ equal }}
    </a-drawer>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { notification } from 'ant-design-vue';
import dayjs from 'dayjs';
import { ref } from 'vue';


const props = defineProps({
    record: Object,
    rec: Number,
})

const form = useForm({
    barcode: [],
})

const emit = defineEmits(['close-drawer']);

const equal = ref(0);


const validate = () => {

    form.transform((data) => ({
        ...data,
        id: props.record.rel_num,
        recnum: props.rec,
        denom_id: props.record.gc.denom_id,
    })).get(route('retail.validate.barcode'), {

        onSuccess: (response) => {

            notification[response.props.flash.status]({
                message: response.props.flash.title,
                description: response.props.flash.msg,
                placement: 'topLeft'
            });
            if (response.props.flash.status == 'success') {
                equal.value += 1;
                form.reset();
            }
            if(props.record.quantity == equal.value){
                emit('close-drawer')
            }

        },

        preserveState: true,
    })
}




</script>
