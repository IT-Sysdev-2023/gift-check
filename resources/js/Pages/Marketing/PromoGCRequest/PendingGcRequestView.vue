<template>
    <AuthenticatedLayout>
        <a-card title="Update Promo GC Request">
            <a-row :gutter="[16, 16]">
                <a-col :span="12">
                    <a-card>
                        <a-form-item label="RFPROM No">
                            <a-input v-model:value="form.reqnum" readonly />
                        </a-form-item>
                        <a-form-item label="Date Requested">
                            <a-input v-model:value="form.dateReq" readonly />
                        </a-form-item>
                        <a-form-item label="Date Needed">
                            <a-date-picker style="width: 100%;" v-model:value="form.dateNeed"  />
                        </a-form-item>
                        <a-form-item label="Remarks">
                            <a-textarea v-model:value="form.remarks" />
                        </a-form-item>
                        <a-form-item label="Promo Group">
                            <a-select ref="select" v-model:value="form.group" style="width: 120px">
                                <a-select-option value="1">1</a-select-option>
                                <a-select-option value="2">2</a-select-option>
                            </a-select>
                        </a-form-item>
                        <a-form-item>
                            <a-table :dataSource="denom" :columns="columns" :pagination="false">
                                <template v-slot:bodyCell="{ column, record }">
                                    <template v-if="column.dataIndex === 'qty'">
                                        <a-input v-model:value="record.qty"></a-input>
                                    </template>
                                </template>
                            </a-table>
                        </a-form-item>
                    </a-card>
                </a-col>
                <a-col :span="12">
                    <!-- {{ props.data[0].pgcreq_datereq }} -->
                    <a-button @click="submit">
                        submit
                    </a-button>
                    <a-card>

                    </a-card>
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/core';
import dayjs from 'dayjs';
import { ref } from 'vue';
import pickBy from "lodash/pickBy";
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    data: Object,
    denom: Object,
    columns: Object,
    denomQty: Object
});
const form = useForm({
    reqnum: props.data[0]?.pgcreq_id,
    dateReq: props.data[0]?.dateRequested,
    dateNeed: dayjs(props.data[0].pgcreq_datereq),
    remarks: props.data[0]?.pgcreq_remarks,
    group: props.data[0]?.pgcreq_group,
});

const submit = () => {

    const denomination = props.denom.map((record) => ({
        quantity: record.qty,
        id: record.denom_id,
    }));

    form.transform((data) => ({
        ...data,
        date: dayjs(data.dateNeed).format('YYYY-MM-DD'),
        denom: pickBy(denomination)
    })).post(route('marketing.promoGcRequest.submit'));

}



</script>
