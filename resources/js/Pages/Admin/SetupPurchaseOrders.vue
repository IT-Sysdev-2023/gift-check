<template>
    <AuthenticatedLayout>
        <a-typography-text keyboard>{{ title }}</a-typography-text>
        <a-row :gutter="[16, 16]" class="mt-2">
            <a-col :span="8">
                <a-card>
                    <a-table :pagination="false" size="small" bordered :data-source="denom" :columns="[
                        {
                            title: 'Denomination',
                            dataIndex: 'denomination_format',
                            width: '50%'
                        },
                        {
                            title: 'Qty',
                            dataIndex: 'qty',
                            width: '50%'
                        }
                    ]">
                    </a-table>
                </a-card>
                <a-divider>
                    Requisition Number
                </a-divider>
                <a-form-item has-feedback :help="form.errors.reqno"
                    :validate-status="form.invalid('reqno') ? 'error' : ''">
                    <a-input v-model:value="form.reqno" @change="form.validate('reqno')" class="text-center"
                        style="width: 100%;" size="large" />
                </a-form-item>
                <a-button class="mt-2" type="primary" block @click="submit">
                    <FastForwardOutlined /> Submit Requisition Number
                </a-button>
            </a-col>
            <a-col :span="16">
                <a-card>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Receiving No">{{ record.data.recno
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Transaction Date">{{ record.data.transdate
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Reference No">{{ record.data.refno
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Purchase Order No">{{ record.data.pon
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Purchase Date">{{ record.data.purdate
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Reference PO No">{{ record.data.refpon
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Payment Terms">{{ record.data.payterms
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Location Code">{{ record.data.locode
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Department Code">{{ record.data.depcode
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Supplier Name">{{ record.data.supname
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Mode of Payment">{{ record.data.mop
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Remarks">{{ record.data.remarks
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Prepared By">{{ record.data.prepby
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Checked  By">{{ record.data.checkby
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="SRR Type">{{ record.data.srrtype
                            }}</a-descriptions-item>
                    </a-descriptions>
                </a-card>
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from 'laravel-precognition-vue';
import { notification } from 'ant-design-vue';
import { router } from '@inertiajs/core';

const props = defineProps({
    record: Object,
    denom: Object,
    title: String,
});

const form = useForm('post', route('admin.submit.po.to.iad'), {
    record: props.record,
    denom: props.denom,
    name: props.title,
    reqno: null
});

const submit = () => {
    form.submit({
        onSuccess: ({ data }) => {
            console.log(data.msg);
            notification[data.status]({
                message: data.msg,
                description: data.title,
            });

            router.visit(route('admin.purchase.order.details'))
        }
    });
}
</script>
