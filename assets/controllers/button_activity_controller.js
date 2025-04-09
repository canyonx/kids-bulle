import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
	static targets = ["icon"]

	async toggle(event) {
		event.preventDefault()

		const activityId = this.element.dataset.activityId
		const childId = this.element.dataset.childId
		const isActive = this.element.classList.contains("active")
		const action = isActive ? "remove" : "add"

		this.setLoading(true)

		try {
			const response = await fetch("/api/action", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					"X-Requested-With": "XMLHttpRequest",
				},
				body: JSON.stringify({
					action: action,
					child: childId,
					activity: activityId,
				}),
			})

			if (!response.ok) {
				console.error("API Error:", await response.text())
				return
			}

			await response.json()

			this.element.classList.toggle("active")

			// Mise à jour de l’icône
			if (this.hasIconTarget) {
				this.iconTarget.classList.remove("fa-circle-plus", "fa-circle-xmark")
				this.iconTarget.classList.add(action === "add" ? "fa-circle-xmark" : "fa-circle-plus")
			}

            // Mise à jour du badge
            const globalBadge = document.getElementById("child-count")
            if (globalBadge) {
                const current = parseInt(globalBadge.textContent)
                globalBadge.textContent = action === "add" ? current + 1 : current - 1
            }

            const frameIds = ["activity-children", "activity-not-enrolled"]

            frameIds.forEach(id => {
                const frame = document.getElementById(id)
                if (frame) {

                    Turbo.visit(window.location.href, { action: 'replace' })
                }
            })



		} catch (error) {
			console.error("Fetch error:", error)
		} finally {
			this.setLoading(false)
		}

	}

	setLoading(loading) {
		if (this.hasIconTarget) {
			this.iconTarget.classList.toggle("fa-spin", loading)
		}
	}
}
