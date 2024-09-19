<template>
    <AuthenticatedLayout>
        <a-card>
            <a-row :gutter="[16, 16]">
                <a-col :span="16">
                    <a-row :gutter="[16, 16]">
                        <a-col :span="12">
                            <a-form :layout="'jorizontal'" v-bind="formItemLayout">
                                <a-form-item label="Customs Barcode">
                                    <a-select v-model:value="form.barcode">
                                        <a-select-option v-for="bar in barcodeStart" :value="bar.id">{{ bar.coup_barcode_start }}</a-select-option>
                                    </a-select>
                                </a-form-item>
                                <a-form-item label="Transaction No">
                                    <a-input :value="trans" placeholder="input placeholder" />
                                </a-form-item>
                                <a-form-item label="Payment Date">
                                    <a-input :value="dayjs().format('MMM DD YYYY ')" />
                                </a-form-item>
                                <a-form-item label="Date Needed">
                                    <a-date-picker @change="handleDateChange" style="width: 100%;"
                                        placeholder="Select Date" />
                                </a-form-item>
                                <a-form-item label="Upload Scan Copy">
                                    <ant-upload-image @handle-change="handleImage" />
                                </a-form-item>
                            </a-form>
                        </a-col>
                        <a-col :span="12">
                            <a-form :layout="'jorizontal'" v-bind="formItemLayout">
                                <a-form-item label="Look Up Customer">
                                    <ant-select :options="options" @handle-change="handleChange" />
                                </a-form-item>
                                <a-form-item label="Ar No">
                                    <a-input v-model:value="form.ar" />
                                </a-form-item>
                                <a-form-item label="Payment Type">
                                    <a-select ref="select" v-model:value="value1" placeholder="Select Payment Method"
                                        style="width: 100%" @focus="focus" @change="handleChange">
                                        <a-select-option v-for="type in paymethod" v-model:value="type.value">{{
                                            type.label }}</a-select-option>
                                    </a-select>
                                </a-form-item>
                                <a-form-item label="Remarks">
                                    <a-textarea v-model:value="form.remarks"></a-textarea>
                                </a-form-item>
                            </a-form>
                        </a-col>
                    </a-row>
                </a-col>
                <a-col :span="8">
                    <ant-denom-item :form="form.denomination" @submit-form="onSubmit" />
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { ref } from 'vue';
import pickBy from "lodash/pickBy";

const props = defineProps({
    trans: Number,
    options: Object,
    barcodeStart: Object,
});

const form = useForm({
    company: null,
    ar: null,
    date: null,
    remarks: null,
    denomination: [],
    file: [],
});
const onSubmit = () => {
    router.post(route('treasury.coupon.submit'), {
        ...pickBy(form)
    })
}
const handleChange = (value, data) => {
    form.company = value;
}
const handleDateChange = (obj, str) => {
    form.date = str;
}
const handleImage = (file) => {
    form.file = file.fileList;
}

const paymethod = ref(
    [
        {
            value: "1",
            label: "Cash",
        },
        {
            value: "2",
            label: "Check",
            disabled: true,
        },
        {
            value: "3",
            label: "JV",
        },
        {
            value: "4",
            label: "AR",
        },
        {
            value: "5",
            label: "On Account",
        },
    ]
)
</script>
