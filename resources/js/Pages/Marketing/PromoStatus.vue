<template>
    <Head title="Promo GC Status" />
    <a-card>
        <a-card class="mb-2" title="Promo GC Status"></a-card>
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
        data: Array,
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
