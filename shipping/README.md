# Shipping rates awaiting configuration

`rates-draft.json` preserves the supplied rates as `[minimum weight, maximum weight, PHP fee]`. It is reference data only and is not loaded by the plugin or deployment scripts.

Before implementing checkout rates, confirm:

- Packaging weight per order: confirmed at 0.30 kg per order. Kilograms and 0.01 kg per mask are confirmed; minimum order is 3 masks.
- Handling above 6 weight units for NCR/Luzon/Visayas and above 10 for Mindanao.
- Boundary convention: proposed non-overlapping bands are `0 < weight <= first upper limit`, then `previous upper limit < weight <= next upper limit`. This resolves overlapping endpoints in the first three zones and the small gaps in Mindanao without guessing at checkout.
- Complete geographic matching: supplied zone summaries are truncated and include postcodes. Inspect existing Good Fruit zones and reproduce explicit Philippine province/postcode coverage before enabling rates. NCR must take precedence over broader Luzon rules.

No free shipping is authorized. Missing product weight must not silently qualify for the cheapest bracket. No rate should be extrapolated beyond the supplied table. Existing shipping settings need a backup and review before replacement.
