<template>
    <a-breadcrumb class="mb-4">
        <a-breadcrumb-item>Dashboard</a-breadcrumb-item>
        <a-breadcrumb-item>Barcode Checker</a-breadcrumb-item>
    </a-breadcrumb>
    <a-row :gutter="[16, 16]">
        <a-col :span="8">
            <BarcodeForm :date="date" />
            <Statistics :counts="count" class="mt-5" />
        </a-col>
        <a-col :span="16">
            <a-card>
                <a-table size="small" :data-source="data" :columns="columns" :pagination="false" />
                <div class="mt-4" :style="{
                    height: '250px',
                    overflow: 'hidden',
                    position: 'relative',
                    border: '1px solid #ebedf0',
                    borderRadius: '2px',
                    padding: '48px',
                    textAlign: 'center',
                    background: '#fafafa',
                }" @close="onClose">
                    Search Barcode
                    <div style="margin-top: 16px">
                        <a-input-search v-model:value="form.search" allow-clear show-count placeholder="input search text" style="width: 350px"
                            @keyup.enter="onSearch" />
                    </div>
                    <a-drawer title="Search Result" placement="right" :closable="false" :open="open"
                        :get-container="false" :style="{ position: 'absolute' }" @close="onClose">
                        <div v-if="search.length != 0">
                            <a-alert message="Barcode Found" type="success" show-icon />
                            <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                                <a-descriptions-item style="width: 50%;" label="Barcode" class="text-right">{{
                                    search[0]?.bcheck_barcode
                                    }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                                <a-descriptions-item style="width: 50%;" label="Scanned By" class="text-right">{{
                                    search[0]?.users.full_name
                                    }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                                <a-descriptions-item style="width: 50%;" label="Scanned Date" class="text-right">{{
                                    search[0]?.bcheck_date
                                    }}</a-descriptions-item>
                            </a-descriptions>
                        </div>
                        <div v-else>
                            <a-alert message="Opss Sorry Barcode Not Found" type="error" show-icon />
                        </div>
                    </a-drawer>
                </div>
            </a-card>
        </a-col>
    </a-row>
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        columns: Array,
        count: Array,
        date: String,
        search: Object,
    },
    data() {
        return {
            open: false,
            form: {
                search: null,
            }
        }
    },
    methods: {
        onSearch() {
            this.$inertia.get(route('custodian.barcode.checker'), {
                search: this.form.search
            }, {
                onSuccess: () => {
                    this.open = true;
                },
                preserveState: true,
                preserveScroll: true,
            })
        },
        onClose() {
            this.open = false;
        }
    }

}
</script>
