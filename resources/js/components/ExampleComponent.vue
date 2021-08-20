<template>

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Example Component</div>
                    <div class="panel-body">
                        I'm an example component!
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import gql from 'graphql-tag'


export default {
    apollo() {},
    data() {
        return {
            name: ""
        }
    },
    mounted() {
        console.log('Component mounted.')
        this.addTag()
    },
    methods: {
        async addTag() {
            // Call to the graphql mutation
            const response = await this.$apollo.query({
                // Query
                query: gql`
			query
				getProject {
					getProject(id: 1){
						id
					}
				}`,


                // Parameters
                variables: {
                    label: this.name,
                },
            })

            console.log(response)
        }
    }

}
</script>
