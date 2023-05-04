import BayesClassifier from "bayes-classifier";
import { get } from "axios";

const Classifier = {
	async install(Vue, options) {
		let classifier = false,
			savedClassifier = false;

		function ensureClassifier() {
			if (!savedClassifier) return;

			if (!classifier) {
				classifier = new BayesClassifier();
				classifier.restore(savedClassifier);
			}
		}

		Vue.prototype.loadClassifier = async function(onProgress) {
			const data = await get("/api/classifier.json?v=" + classifierJSON, {
				onDownloadProgress: progressEvent => {
					const percentage = Math.floor((progressEvent.loaded / progressEvent.total) * 100);

					onProgress(percentage);
				}
			});

			savedClassifier = data.data;
		};

		Vue.prototype.classifyCharacter = function (character) {
			ensureClassifier();

			if (!classifier) return false;

			const text = character.name + "\n" + character.backstory;

			return classifier.classify(text);
		};
	},
}

export default Classifier;
