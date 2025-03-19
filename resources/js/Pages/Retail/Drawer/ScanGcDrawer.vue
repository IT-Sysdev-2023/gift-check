<template>
    <a-modal :footer="false" class="custom-class" root-class-name="root-class-name" title="Scan Barcode Drawer"
        placement="right" @close="clear">

        <a-alert v-if="error.status" :message="error.msg" :type="error.status" class="mb-2" show-icon />

        <div class="flex justify-center">
            <a-typography-text keyboard>Scan Barcode:</a-typography-text>
        </div>
        <a-input-number  class="p-2 text-3xl" placeholder="Enter Barcode" v-model:value="form.barcode" allow-clear
            style="width: 100%" @keyup.enter="validate" size="large" @change="barcodeHandler" />
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
    </a-modal>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { notification } from 'ant-design-vue';
import dayjs from 'dayjs';
import { ref } from 'vue';


const props = defineProps({
    record: Object,
    rec: Number,
    data: Object
})

const form = useForm({
    barcode: [],
})

const emit = defineEmits(['close-drawer']);

const error = ref({});

const validate = () => {
    form.transform((data) => ({
        ...data,
        id: props.record.rel_num,
        recnum: props.rec,
        denom_id: props.record.gc.denom_id,
    })).get(route('retail.validate.barcode'), {

        onSuccess: (response) => {

            error.value = response.props.flash;

            notification[response.props.flash.status]({
                message: response.props.flash.title,
                description: response.props.flash.msg,
                placement: 'topLeft'
            });

            if (response.props.flash.status == 'success') {
                form.reset();
            }
            if (response.props.flash.status == 'error') {
                form.reset();
            }
            if (response.props.flash.status == 'warning') {
                form.reset();
            }

            if (props.record.quantity == props.data.release[props.record.gc.denom_id][0].scanned) {
                emit('close-drawer')
            }


        },
        onError: () => {
            form.reset();
        },

        preserveState: true,
    })
}
const clear = () => {
    error.value = {}
}




</script>
