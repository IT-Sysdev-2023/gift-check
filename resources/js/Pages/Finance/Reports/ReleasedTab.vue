<template>

    <ProgressBar v-if="isGenerating" :progressBar="progressBar" />

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
                    {{
                        isGenerating ? "Generating " + (formReleased.extension === 'pdf' ? 'PDF' : 'Excel') + "inprogress..":
                        "Generate Spgc Releasing " + (formReleased.extension == null ? '' : formReleased.extension === 'pdf'
                        ? 'PDF' : 'Excel')}}
                </a-button>
            </div>
        </div>
    </div>
    <a-tabs v-model:activeKey="activeKey" tabPosition="left" class="mt-10" type="card">
        <a-tab-pane key="1" tab="Released Per Customers">
            <p class="text-center underline">Released Report Per Customers Table</p>
            <a-table class="mt-5" size="small" :pagination="false" :data-source="datarecordsReleased.dataCus.data"
                :columns="datacolumnsReleased.columnsCus" bordered >
            </a-table>
            <Pagination :datarecords="datarecordsReleased?.dataCus" class="mt-5" />
        </a-tab-pane>
        <a-tab-pane key="2" tab="Released Per Barcodes">
            <p class="text-center underline">Released Report Per Barcodes Table</p>
            <a-table class="mt-5" size="small" :pagination="false" :data-source="datarecordsReleased.dataBar.data"
                :columns="datacolumnsReleased.columnsBar" bordered>
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
            isGenerating: false,
            progressBar: {
                percentage: 0,
                message: "",
                totalRows: 0,
                currentRow: 0,
            },
        }
    },
    methods: {
        generateApprovedReleasedReports() {
            this.isGenerating = true;
            this.$inertia.get(route('finance.released.spgc.pdf.excel'), {
                dateRange: this.filtersReleased.dateRange ? this.filtersReleased.dateRange.map((date) => dayjs(date).format('YYYY-MM-DD')) : [],
                ext: this.formReleased.extension,
                approvedType: this.formReleased.approvedType
            }, {
                preserveState: true,
            });
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
    },
    mounted() {
        this.$ws.private(`generating-app-release-reports.${this.$page.props.auth.user.user_id}`)
            .listen(".generate-app-rel", (e) => {
                this.progressBar = e;
                this.isGenerating = true;
            });
    }
}
</script>
