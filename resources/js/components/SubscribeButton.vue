<template>

	<button :class="classes" @click="subscribe">Subscribe</button>


</template>

<script>
	export default {

		props: ['active'],

		computed: {

			classes() {
				return ['btn', this.active ? 'btn-dark' : 'btn-outline-dark'];
			}
		},

		methods: {

			subscribe() {

				axios[(this.active ? 'delete' : 'post')](location.pathname + '/subscriptions')
					.then(
						this.active = ! this.active
				);

			}
		}
	};
</script>
