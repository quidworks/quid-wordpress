quid.createFAB({
  // Required
  apiKey: dataJS.apiKey,
  baseURL: dataJS.baseURL,
  currency: dataJS.currency,
  productID: dataJS.id,
  productName: dataJS.name,
  productURL: dataJS.url,
  productDescription: dataJS.description !== "" ? dataJS.description : 'Thanks for the support!',
  // Optional
  position: dataJS.position,
  minAmount: dataJS.min,
  maxAmount: dataJS.max,
  amount: dataJS.amount,
  text: dataJS.text,
  paidText: dataJS.paid,
  palette: dataJS.palette,
  demo: dataJS.demo === "true",
  reminder: dataJS.reminder === "true",
});