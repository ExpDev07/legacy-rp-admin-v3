import BayesClassifier from "bayes-classifier";
import { get } from "axios";

function _getTrainingData(profile) {
	let creationTime = 'long';

	if (profile.creationTime < 2 * 60) {
		creationTime = 'short';
	} else if (profile.creationTime < 5 * 60) {
		creationTime = 'medium';
	} else if (profile.creationTime < 10 * 60) {
		creationTime = 'decent';
	}

	const modelName = models[profile.pedModelHash] || 'unknown';

	return `${profile.firstName} ${profile.lastName}
${modelName}
${profile.gender} born ${profile.dateOfBirth}
${creationTime}
${profile.backstory}`;
}

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

			const text = _getTrainingData(character);

			console.log(text);

			return classifier.classify(text);
		};
	},
}

export default Classifier;
