<template>
    <a-range-picker style="width: 400px;" v-model:value="form.dateRange" />
    <a-tabs v-model:activeKey="activeKey" class="mt-3" type="card">
        <a-tab-pane key="1" tab="Data Customers">
            <a-table class="mt-5" size="small" :data-source="datarecords.dataCus.data" :columns="datacolumns.columnsCus"
                :pagination="false">
            </a-table>
            <Pagination :datarecords="datarecords?.dataCus" class="mt-5" />
        </a-tab-pane>
        <a-tab-pane key="2" tab="Data Barcodes">
            <a-table class="mt-5" size="small" :data-source="datarecords.dataBar.data" :columns="datacolumns.columnsBar"
                :pagination="false">
            </a-table>
            <!-- {{ datarecords.dataBar.total }} -->
            <Pagination :datarecords="datarecords?.dataBar" class="mt-5" />
        </a-tab-pane>
    </a-tabs>
</template>

<script>
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
export default {
    props: {
        datarecords: Object,
        datacolumns: Object,
    },
    data() {
        return {
            form: {
                dateRange: []
            },
            activeKey: '1'
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                const formattedDate = this.form.dateRange ? this.form.dateRange.map((date) => date.format("YYYY-MM-DD")) : [];
                this.$inertia.get(route('finance.approved.released.reports'), { ...pickBy(this.form), dateRange: formattedDate }, {
                    preserveState: true,
                })
            })
        }
    }
}
</script>
