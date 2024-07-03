<template>

    <ProgressBar v-if="isGenerating" :progressBar="progressBar" />

    <div class="flex justify-between">
        <div>
            <a-range-picker style="width: 400px;" v-model:value="formApproved.dateRange" />
        </div>
        <div class="flex justify-between">
            <div style="border: 1px solid #D8D9DA; display: flex; align-items: center; padding-left: 10px; border-radius: 5px;"
                class="mr-1">
                <a-radio-group v-model:value="formApproved.extension">
                    <a-radio :value="'pdf'">
                        <a-typography-text :keyboard="formApproved.extension === 'pdf'">Generate
                            to PDF</a-typography-text>
                    </a-radio>
                    <a-radio :value="'excel'">
                        <a-typography-text :keyboard="formApproved.extension === 'excel'">Generate to
                            Excel</a-typography-text>
                    </a-radio>
                </a-radio-group>
            </div>
            <div>
                <a-button type="primary" @click="generateApprovedReleasedReports" :disabled="formApproved.extension == null || (datarecordsApproved.dataCus.total <= 0 && datarecordsApproved.dataCus.total <= 0)
                    ">
                    {{
                        isGenerating ? "Generating " +
                            (formApproved.extension === 'pdf' ? 'PDF' : 'Excel') + " inprogress.." :
                            "Generate Spgc Approved " +
                            (formApproved.extension == null ? '' : formApproved.extension === 'pdf'
                                ?
                                'PDF' : 'Excel') }}
                </a-button>
            </div>
        </div>
    </div>

    <a-tabs v-model:activeKey="activeKey" tabPosition="left" class="mt-10" type="card">
        <a-tab-pane key="1" tab="Approved Per Customers">
            <p class="text-center underline"> Approved Reports Per Customers Table</p>
            <a-table class="mt-5" size="small" :data-source="datarecordsApproved.dataCus.data"
                :columns="datacolumnsApproved.columnsCus" :pagination="false">
            </a-table>
            <Pagination :datarecords="datarecordsApproved?.dataCus" class="mt-5" />
        </a-tab-pane>
        <a-tab-pane key="2" tab="Approved Per Barcodes">
            <p class="text-center underline">Approved Reports Per Barcodes Table</p>
            <a-table class="mt-5" size="small" :data-source="datarecordsApproved.dataBar.data"
                :columns="datacolumnsApproved.columnsBar" :pagination="false">
            </a-table>
            <Pagination :datarecords="datarecordsApproved?.dataBar" class="mt-5" />
        </a-tab-pane>
    </a-tabs>

</template>

<script>
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import dayjs from "dayjs";
import ProgressBar from '@/Components/Finance/ProgressBar.vue';
import ProgressBarInner from '@/Components/Finance/ProgressBarInner.vue';
import ProgressHeader from '@/Components/Finance/ProgressHeader.vue';
export default {
    props: {
        datarecordsApproved: Object,
        datacolumnsApproved: Object,
        filtersApproved: Object,
    },
    data() {
        return {
            formApproved: {
                dateRange: this.filtersApproved.dateRange ? this.filtersApproved.dateRange.map((date) => dayjs(date)) : [],
                extension: null,
                approvedType: 'Special External GC Approved',
                key: this.filtersApproved.key,
            },
            isGenerating: false,
            value: null,
            activeKey: '1',
            isGenerating: false,
            isGeneratingInner: false,
            isGeneratingHeader: false,
            progressBar: {
                percentage: 0,
                message: "",
                totalRows: 0,
                currentRow: 0,
            },
            progressBarInner: {
                percentage: 0,
                message: "",
                totalRows: 0,
                currentRow: 0,
            },
            progressBarHeader: {
                percentage: 0,
                message: "",
                totalRows: 0,
                currentRow: 0,
            },
        }
    },
    methods: {
        generateApprovedReleasedReports() {
            this.$inertia.get(route('finance.approved.spgc.pdf.excel'), {
                dateRange: this.filtersApproved.dateRange ? this.filtersApproved.dateRange.map((date) => dayjs(date).format('YYYY-MM-DD')) : [],
                ext: this.formApproved.extension,
                approvedType: this.formApproved.approvedType
            }, {
                preserveState: true,
            });
        }
    },
    watch: {
        formApproved: {
            deep: true,
            handler: throttle(function () {
                const formattedDate = this.formApproved.dateRange ? this.formApproved.dateRange.map((date) => date.format("YYYY-MM-DD")) : [];
                this.$inertia.get(route('finance.approved.released.reports'), { ...pickBy(this.formApproved), dateRange: formattedDate }, {
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
        this.$ws.private(`generating-app-release-reports-inner.${this.$page.props.auth.user.user_id}`)
            .listen(".generate-app-rel-inner", (e) => {
                this.progressBarInner = e;
                this.isGeneratingInner = true;
            });
        this.$ws.private(`generating-app-release-reports-header.${this.$page.props.auth.user.user_id}`)
            .listen(".generate-app-rel-header", (e) => {
                this.progressBarHeader = e;
                this.isGeneratingHeader = true;
            });
    }
}
</script>
