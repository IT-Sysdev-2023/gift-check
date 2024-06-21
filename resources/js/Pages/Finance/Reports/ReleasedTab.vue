<template>
    <div class="flex justify-between">
        <div>
            <a-range-picker style="width: 400px;" v-model:value="formReleased.dateRange" />
        </div>
        <div class="flex justify-between">
            <div style="border: 1px solid #D8D9DA; display: flex; align-items: center; padding-left: 10px; border-radius: 5px;"
                class="mr-1">
                <a-radio-group v-model:value="formReleased
                    .extension">
                    <a-radio :value="'pdf'">
                        <a-typography-text :delete="formReleased.extension === 'pdf'" :mark="formReleased
                            .extension === 'pdf'">Generate
                            to PDF</a-typography-text>
                    </a-radio>
                    <a-radio :value="'excel'">
                        <a-typography-text :delete="formReleased.extension === 'excel'" :mark="formReleased
                            .extension === 'excel'">Generate to Excel</a-typography-text>
                    </a-radio>
                </a-radio-group>
            </div>
            <div>
                <a-button type="primary" @click="generateApprovedReleasedReports" :disabled="formReleased
                    .extension == null || (datarecordsReleased.dataCus.total <= 0 && datarecordsReleased.dataCus.total <= 0)
                    ">
                    generate
                </a-button>
            </div>
        </div>
    </div>
    <a-tabs v-model:activeKey="activeKey" tabPosition="left" class="mt-10" type="card">
        <a-tab-pane key="1" tab="Released Per Customers">
            <p class="text-center underline">Released Report Per Customers Table</p>
            <a-table class="mt-5" size="small" :pagination="false" :data-source="datarecordsReleased.dataCus.data"
                :columns="datacolumnsReleased.columnsCus">
            </a-table>
            <Pagination :datarecords="datarecordsReleased?.dataBar" class="mt-5" />
        </a-tab-pane>
        <a-tab-pane key="2" tab="Released Per Barcodes">
            <p class="text-center underline">Released Report Per Barcodes Table</p>
            <a-table class="mt-5" size="small" :pagination="false" :data-source="datarecordsReleased.dataBar.data"
                :columns="datacolumnsReleased.columnsBar">
            </a-table>
            <Pagination :datarecords="datarecordsReleased?.dataBar" class="mt-5" />
        </a-tab-pane>
    </a-tabs>
</template>
<script>
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import dayjs from "dayjs";
export default {
    props: {
        datarecordsReleased: Object,
        datacolumnsReleased: Object,
        filtersReleased: Object,
    },
    data() {
        return {
            formReleased: {
                dateRange: this.filtersReleased.dateRange ? this.filtersReleased.dateRange.map((date) => dayjs(date)) : [],
                extension: null,
                approvedType: 'special external releasing',
                key: this.filtersReleased.key,
            },
            activeKey: '1',
        }
    },
    watch: {
        formReleased: {
            deep: true,
            handler: throttle(function () {
                const formattedDate = this.formReleased.dateRange ? this.formReleased.dateRange.map((date) => date.format("YYYY-MM-DD")) : [];
                this.$inertia.get(route('finance.approved.released.reports'), { ...pickBy(this.formReleased), dateRange: formattedDate }, {
                    preserveState: true,
                })
            })
        }
    }
}
</script>
