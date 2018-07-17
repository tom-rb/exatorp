<script>
    export default {
        props: ['first_area_old', 'first_job_old', 'second_area_old', 'second_job_old'],

        data() {
            return {
                first_area_selected: this.first_area_old,
                first_job_selected: this.first_job_old,
                second_area_selected: this.second_area_old,
                second_job_selected: this.second_job_old,
            }
        },
    }
</script>
