<template>
    <a-card>
        <a-tabs v-model:activeKey="activeKey">
            
            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <ReconciliationOutlined />
                        Spgc Approved Reports
                    </span>
                </template>

                <ApprovedTab :datarecords="data" :datacolumns="columns" :filters="filters"/>

            </a-tab-pane>
            <a-tab-pane key="2">
                <template #tab>
                    <span>
                        <ProfileOutlined />
                        Spgc Released Reports
                    </span>
                </template>
                Tab 2
            </a-tab-pane>
        </a-tabs>
    </a-card>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApprovedTab from "@/Pages/Finance/Reports/ApprovedTab.vue"
export default {
    layout: AuthenticatedLayout,
    props: {
       data: Object,
       columns: Array,
       filters: Object
    },
    data() {
        return {
            activeKey: '1',
        }
    }

}
</script>
