<template>

    <Head title="Verified GC - Alturas Mall" />
    <a-card>
        <a-card class="mb-2" title="Alturas Mall - Verified GC"></a-card>
        <div class="flex justify-end">
            <a-input-search class="mt-5 mb-5" v-model:value="search" placeholder="input search text here."
                style="width: 300px" @search="onSearch" />
        </div>
        <a-table :dataSource="data.data" size="small" bordered :columns="columns" :pagination="false">
            <template #bodyCell="{ column, record }">
                <!-- {{ record.vs_tf_balance }} -->
                <template v-if="column.key === 'dateVerRev'">
                    {{ record.vs_date }}
                    <div v-if="record.vs_reverifydate !== ''">
                        {{ record.vs_reverifydate }}
                    </div>
                </template>
                <template v-if="column.key === 'verby'">
                    {{ record.verbyFirstname }}, {{ record.verbyLastname }}
                    <div v-if="record.vs_reverifydate !== ''">
                        {{ record.revbyFirstname }} {{ record.revbyLastname }}
                    </div>
                </template>
                <template v-if="column.key === 'customer'">
                    {{ record.customersFirstname }}, {{ record.customersLastname }}
                </template>
                <template v-if="column.key === 'view'">
                    <a-button @click="view(record.vs_barcode)">
                        <EyeOutlined />
                    </a-button>
                </template>
            </template>
        </a-table>
        <pagination class="mt-5" :datarecords="data" />
    </a-card>

</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";

export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        columns: Array,
    },
    data() {
        return {
            search: '',
        };
    },
    methods: {
        view(id) {
            router.get(route('marketing.verifiedgc.view'), {
                barcode: id
            });
        }
    },
    watch: {
        search: {
            deep: true,
            handler: debounce(function () {
                this.$inertia.get(route("verified.gc.alturas.mall"), {
                    search: this.search
                }, {
                    preserveState: true,
                });
            }, 600),
        },
    },
    mounted() {
        console.log("Current Route:", this.currentRoute);
    }
};
</script>
