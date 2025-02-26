<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="10">
                <a-card>
                    <a-descriptions size="small" title="Details" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="RFSEGC#">Cloud Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="DEPARTMENT">Cloud Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="DATE REQUEST">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="TIME REQUESTED">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="DATE NEEDED">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="DATE APPROVED">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="PAYMENT AMOUNT">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="TOTAL DENOMINATION">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="REQUEST REMARKS">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="APPROVED REMARKS">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                </a-card>
                <a-divider>
                    Committee
                </a-divider>
                <a-card>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="REQUESTED BY">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="CHECKED BY">Cloud Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="APPROVED BY">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="PREPARED BY">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                </a-card>

            </a-col>
            <a-col :span="14">
                <a-card>
                    <div class="flex justify-between mb-4">
                        <div>
                            <a-form   layout="inline">
                                <a-form-item label="Count">
                                    <a-input style="width: 150px;" class="text-center" placeholder="Username">
                                    </a-input>
                                </a-form-item>
                            </a-form>
                        </div>
                        <div>
                            <a-checkbox>
                                Select all
                            </a-checkbox>
                        </div>
                    </div>
                    <a-table bordered size="small" :columns="columns">

                    </a-table>
                </a-card>
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import { ref } from 'vue';

const columns = ref([
    {
        title: 'First Name',
        dataIndex: 'name',
        key: 'name',
    },
    {
        title: 'Last Name',
        dataIndex: 'age',
        key: 'age',
    },
    {
        title: 'Ext',
        dataIndex: 'address',
        key: 'address',
    },
    {
        title: 'Denomination',
        dataIndex: 'address',
        key: 'address',
    },
    {
        title: 'Barcode',
        dataIndex: 'address',
        key: 'address',
    },
    {
        title: 'Action',
        dataIndex: 'address',
        key: 'address',
    },
])
</script>
