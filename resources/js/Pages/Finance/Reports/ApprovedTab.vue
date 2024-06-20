<template>
    <div class="flex justify-between">
        <div>
            <a-range-picker style="width: 400px;" v-model:value="form.dateRange" />
        </div>
        <div class="flex justify-between">
            <div style="border: 1px solid #D8D9DA; display: flex; align-items: center; padding-left: 10px; border-radius: 5px;"
                class="mr-1">
                <a-radio-group v-model:value="form.extension">
                    <a-radio :value="'pdf'">
                        <a-typography-text :delete="form.extension === 'pdf'" :mark="form.extension === 'pdf'">Generate
                            to PDF</a-typography-text>
                    </a-radio>
                    <a-radio :value="'excel'">
                        <a-typography-text :delete="form.extension === 'excel'"
                            :mark="form.extension === 'excel'">Generate to Excel</a-typography-text>
                    </a-radio>
                </a-radio-group>
            </div>
            <!-- {{ form.extension }} -->
            <div>
                <a-button type="primary" @click="generateApprovedReleasedReports" :disabled="form.extension == null || (datarecords.dataCus.total <= 0 && datarecords.dataCus.total <= 0)
                    ">
                    {{ isGenerating ? "Generating " + (form.extension === 'pdf' ? 'PDF' : 'Excel') + " in progress..":
                        "Generate Spgc Approved " + (form.extension == null ? '' :form.extension === 'pdf' ? 'PDF' : 'Excel')}}
                </a-button>
            </div>
        </div>
    </div>
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
            <Pagination :datarecords="datarecords?.dataBar" class="mt-5" />
        </a-tab-pane>
    </a-tabs>
</template>

<script>
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import dayjs from "dayjs";
export default {
    props: {
        datarecords: Object,
        datacolumns: Object,
        filters: Object,
    },
    data() {
        return {
            form: {
                dateRange: this.filters.dateRange ? this.filters.dateRange.map((date) => dayjs(date)) : [],
                extension: null,
            },
            value: null,
            activeKey: '1',
            isGenerating: false,
        }
    },
    methods: {
        generateApprovedReleasedReports() {
            this.isGenerating = true;
            this.$inertia.get(route('finance.approved.spgc.pdf.result'), {
                dateRange: this.filters.dateRange ? this.filters.dateRange.map((date) => dayjs(date).format('YYYY-MM-DD')) : [],
                ext: this.form.extension,
            }, {
                preserveState: true,
            });
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
