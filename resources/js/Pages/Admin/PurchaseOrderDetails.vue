<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <a-list item-layout="horizontal" :data-source="record" @click="setup(record)">
                    <template #renderItem="{ item }">
                        <a-card size="small">
                            <a-list-item>
                                <a-list-item-meta description="Click Details...">
                                    <template #title>
                                        <a>{{ item }}</a>
                                    </template>
                                    <template #avatar>
                                        <PaperClipOutlined />
                                    </template>
                                </a-list-item-meta>
                            </a-list-item>
                        </a-card>
                    </template>
                </a-list>
            </a-col>
            <a-col :span="16">
                <!-- {{ podetails }} -->
                <a-card>
                    <a-table :data-source="podetails.data" :columns="columns" bordered size="small" :pagination="false"
                        :rowKey="record => record.id" expandable="{ expandedRowRender }">
                        <template #expandedRowRender="{ record }">
                            <a-card>
                                <a-row :gutter="[16, 16]">
                                    <a-col :span="12">
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;"
                                                label="Reference Purchase Order No.">{{
                                                    record.ref_po_no
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;" label="Deparment Code">{{
                                                record.dep_code
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;" label="Location Code.">{{
                                                record.loc_code
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;" label="Receiving No.">{{
                                                record.rec_no
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;" label="Reference No.">{{
                                                record.ref_no
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;" label="Purchase Date">{{
                                                record.purDate
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;" label="Transaction Date">{{
                                                record.transDate
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;" label="Pay Terms">{{
                                                record.pay_terms
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;" label="Remarks.">{{ record.remarks
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                    </a-col>
                                    <a-col :span="12">
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;" label="Checked By">{{ record.check_by
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;" label="Prepared By">{{
                                                record.prep_by
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                        <a-table bordered :pagination="false" size="small" class="mt-2"
                                            :data-source="record.requis_form_denom" :columns="[
                                                {
                                                    title: 'Fad Item No.',
                                                    dataIndex: 'denom_no',
                                                },
                                                {
                                                    title: 'Quantity',
                                                    dataIndex: 'quantity',
                                                },
                                            ]">

                                        </a-table>
                                    </a-col>
                                </a-row>

                            </a-card>
                        </template>
                        <template #expandColumnTitle>
                            <span style="color: #179BAE">Details</span>
                        </template>

                        <!-- <template #bodyCell="{ column, record }">
                            <template v-if="column.key === 'action'">
                                <a-button size="small" type="primary" @click="edit(record.id)">
                                    <ImportOutlined />
                                    Edit
                                </a-button>
                            </template>
                        </template> -->
                    </a-table>
                    <pagination class="mt-3" :datarecords="podetails"/>
                </a-card>
            </a-col>
        </a-row>
        <!-- {{ record }} -->
    </AuthenticatedLayout>
</template>
<script setup>
import { router } from '@inertiajs/core';

defineProps({
    record: Object,
    podetails: Object,
    columns: Array,
});

const setup = (name) => {
    router.get(route('admin.setup', name));
}
</script>

<style scoped>
.card {
    cursor: pointer;
}

.card:hover {
    color: #0D92F4;
}
</style>
