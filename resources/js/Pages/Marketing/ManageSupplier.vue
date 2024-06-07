<template>
    <a-card>
        <a-card class="mb-2" title="Manage Supplier"></a-card>
        <div class="flex justify-end">
            <a-button @click="showModal">
                <PlusOutlined /> Add New Supplier
            </a-button>
            <a-modal v-model:open="open" title="Add New Supplier" @ok="handleOk">
                <a-form>
                    <a-form-item label="Company Name">
                        <a-input />
                    </a-form-item>
                    <a-form-item label="Account Name">
                        <a-input />
                    </a-form-item>
                    <a-form-item label="Contact Person">
                        <a-input />
                    </a-form-item>
                    <a-form-item label="Contact Number">
                        <a-input />
                    </a-form-item>
                    <a-form-item label="Address">
                        <a-input />
                    </a-form-item>
                </a-form>
            </a-modal>
        </div>
        <div class="flex justify-end">
            <a-input-search class="mt-5 mb-5" v-model:value="search" placeholder="input search text here."
                style="width: 300px" @search="onSearch" />
        </div>

        <a-table :dataSource="data.data" :columns="columns" :pagination="false" />

        <pagination class="mt-5" :datarecords="data" />
    </a-card>

</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import debounce from "lodash/debounce";
import { PlusOutlined } from '@ant-design/icons-vue';

export default {
    layout: AuthenticatedLayout,
    PlusOutlined,
    props: {
        data: Object,
        columns: Array,
    },
    data() {
        return {
            search: '',
            open: false
        }
    },
    methods: {
        showModal() {
            this.open = true;
        },
        handleOk() {
            this.open = false;
        },
        handleCancel() {
            this.open = false;
        }
    },
    watch: {
        search: {
            deep: true,
            handler: debounce(function () {
                // console.log(this.search);
                // const formattedDate = this.form.date ? this.form.date.map(date => date.format('YYYY-MM-DD')) : [];
                this.$inertia.get(route("verified.gc.alturas.mall"), {
                    search: this.search
                }, {
                    preserveState: true,
                });
            }, 600),
        },
    },
}
</script>
