<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <a-card v-for="item in record" class="card" @click="setup(item)">
                    <div class="flex justify-between">
                        <div style="cursor: pointer;">
                            <span style="cursor: pointer; font-weight: bold;">
                                <FileTextOutlined /> &nbsp; {{ item }}
                            </span>
                        </div>
                        <div>
                            <ArrowRightOutlined style="cursor: pointer;" />
                        </div>
                    </div>
                </a-card>
            </a-col>
            <a-col :span="16">
                <a-card>
                    <a-table :data-source="podetails" :columns="columns" bordered size="small" :pagination="false"
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
                                                record.pur_date
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;" label="Transaction Date">{{
                                                record.trans_date
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;" label="Remarks.">{{ record.remarks
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                    </a-col>
                                    <a-col :span="12">
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;" label="Check By">{{ record.check_by
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                        <a-descriptions size="small" layout="horizontal" bordered>
                                            <a-descriptions-item style="width: 50%;" label="Prepare By">{{
                                                record.prep_by
                                                }}</a-descriptions-item>
                                        </a-descriptions>
                                        <a-table bordered :pagination="false" size="small" class="mt-2"
                                            :data-source="record.denomdata" :columns="[
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

                        <template #bodyCell="{ column, record }">
                            <template v-if="column.key === 'action'">
                                <a-button @click="edit(record.id)">
                                    <ImportOutlined />
                                    Edit
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </a-card>
            </a-col>
        </a-row>
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
