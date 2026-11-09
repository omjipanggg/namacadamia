export const formatDate = (date) => {
	return new Intl.DateTimeFormat("en-US", {
		dateStyle: "long",
		timeStyle: "short",
	}).format(new Date(date));
};

export const formatDateTimeReceipt = (dateTime) => {
	if (!dateTime) return "";
	const date = new Date(dateTime);

	const pad = (num) => String(num).padStart(2, "0");

	const day = pad(date.getDate());
	const month = pad(date.getMonth() + 1);
	const year = date.getFullYear();

	const hours = pad(date.getHours());
	const minutes = pad(date.getMinutes());
	const seconds = pad(date.getSeconds());

	return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
};

export const formatDateISO = (isoString) => {
	const [year, month, day] = isoString.split("-");
	const date = new Date(year, month - 1, day);
	const monthName = date.toLocaleDateString("en-US", { month: "short" });
	return `${day}-${monthName}-${year}`;
};

export const formatDateTime = (date) => {
	return new Intl.DateTimeFormat("en-US", {
		year: "numeric",
		month: "long",
		day: "numeric",
		hour: "2-digit",
		minute: "2-digit",
	}).format(new Date(date));
};

export const formatMoney = (amount) => {
	const formattedAmount = new Intl.NumberFormat("id-ID", {
		style: "currency",
		currency: "IDR",
		minimumFractionDigits: 2,
	}).format(amount);

	return formattedAmount.replace(/\s/g, "");
};

export const formatMKNumber = (num) => {
	if (num >= 1_000_000) {
		return (num / 1_000_000).toFixed(1).replace(/\.0$/, "") + "M";
	}
	if (num >= 1_000) {
		return (num / 1_000).toFixed(1).replace(/\.0$/, "") + "K";
	}
	return num.toString();
};

export const formatToInputString = (value) => {
	const dateObject = value ? new Date(value) : new Date();

	if (isNaN(dateObject.getTime())) return;

	const year = dateObject.getFullYear();
	const month = String(dateObject.getMonth() + 1).padStart(2, "0");
	const day = String(dateObject.getDate()).padStart(2, "0");
	const hours = String(dateObject.getHours()).padStart(2, "0");
	const minutes = String(dateObject.getMinutes()).padStart(2, "0");

	return `${year}-${month}-${day}T${hours}:${minutes}`;
};

export const formatNumber = (value, trail = true) => {
	if (value === null || value === undefined || value === "") {
		return "";
	}

	let str = value.toString();

	if (!trail) {
		str = str.replace(/\.0+$|\.00$/, "");
	}

	const parts = str.split(".");

	parts[0] = parts[0].replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");

	return parts.join(",");
};

export const cleanNumber = (value) => {
	if (!value) return "";
	const cleaned = value.toString().replace(/[^0-9]/g, "");
	return cleaned;
};

export const sentenceCase = (string) => {
	return string.charAt(0).toUpperCase() + string.slice(1);
};

export const parseErrors = (errorResponse) => {
	if (!errorResponse) return [];

	const messages = [];

	if (errorResponse.errors && typeof errorResponse.errors === "object") {
		Object.values(errorResponse.errors).forEach((errArray) => {
			if (Array.isArray(errArray)) {
				messages.push(...errArray);
			} else if (typeof errArray === "string") {
				messages.push(errArray);
			}
		});
	}

	if (messages.length === 0 && errorResponse.message) {
		messages.push(errorResponse.message);
	}

	return messages;
};

export const chooseUnused = (array, usedSet) => {
	// const choose = (array) => array[Math.floor(Math.random() * array.length)];
	const available = array.filter((value) => !usedSet.has(value));
	if (available.length === 0) return null;
	const chosen = available[Math.floor(Math.random() * available.length)];
	usedSet.add(chosen);
	return chosen;
};

export const variantLabel = (variants) => {
	return variants.map((variant) => variant.value).join(" • ");
};
