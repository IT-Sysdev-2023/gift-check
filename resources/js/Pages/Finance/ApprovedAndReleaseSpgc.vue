<template>
    <a-card>
        <a-tabs v-model:activeKey="activeKey" @change="handlChangeTab">
            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <ReconciliationOutlined />
                        Spgc Approved Reports
                    </span>
                </template>

                <ApprovedTab :datarecordsApproved="data" :datacolumnsApproved="columns" :filtersApproved="filters" />

            </a-tab-pane>
            <a-tab-pane key="2">
                <template #tab>
                    <span>
                        <ProfileOutlined />
                        Spgc Released Reports
                    </span>
                </template>
                <ReleasedTab :datarecordsReleased="data" :datacolumnsReleased="columns" :filtersReleased="filters" />
            </a-tab-pane>
        </a-tabs>
    </a-card>
    <!-- hello -->
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApprovedTab from "@/Pages/Finance/Reports/ApprovedTab.vue"
import ReleasedTab from "@/Pages/Finance/Reports/ReleasedTab.vue"
export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        columns: Array,
        filters: Object,
    },
    data() {
        return {
            activeKey: this.filters.key,
        }
    },
    methods: {
        handlChangeTab(key) {
            if (['1', '2'].includes(key)) {
                this.$inertia.get(route('finance.approved.released.reports'), {
                    key: key
                });
            }

        }
    }


}
</script>
