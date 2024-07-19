<template>
    <a-tabs v-model:activeKey="activeKey" type="card">
        <a-tab-pane key="1">
            <template #tab>
                <span>
                    <CheckCircleFilled />
                    Promo Gc Approved Table
                </span>
            </template>
            <a-card>
                <div class="flex justify-end">
                    <a-input-search v-model:value="form.search" placeholder="input search text" style="width: 300px"
                        @search="onSearch" class="mb-2" />
                </div>
                <a-table size="small" bordered :data-source="data.data" :columns="columns"
                    :pagination="false"></a-table>
                <pagination-resource class="mt-4" :datarecords="data" />
            </a-card>
        </a-tab-pane>
    </a-tabs>
</template>
<script>
import Authenticatedlayout from "@/Layouts/AuthenticatedLayout.vue";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";

export default {
    layout: Authenticatedlayout,
    props: {
        data: Object,
        columns: Object,
    },
    data() {
        return {
            form: {
                search: ''
            }
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get(
                    route(route().current()), {
                    ...pickBy(this.form)
                }, {
                    preserveState: true,
                }
                );
            }, 150),
        },
    }
};
</script>
