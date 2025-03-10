var mergeSortThis = function mergeSortThis(numbersArray){
	//return the one elem
	if(numbersArray.length <= 1) return numbersArray;

	//divide et impera
	var leftChunk = numbersArray.slice(0,numbersArray.length/2);
	var righChunk = numbersArray.slice(numbersArray.length/2);
	var sortedLeftChunk = mergeSortThis(leftChunk);
	var sortedRightChunk = mergeSortThis(righChunk);
	
	var mergeThese = function mergeThese(leftChunkToBeSorted, rightChunkToBeSorted){
		var endResult = [], ll = 0; rr = 0;

		//iterate until endResult is filled with elems
		while(endResult.length < (leftChunkToBeSorted.length + rightChunkToBeSorted.length) ){
			// if leftChunkToBeSorted poured all its elems,
			// pour in the rightChunkToBeSorted elements
			if(ll === leftChunkToBeSorted.length) {
				endResult = endResult.concat(rightChunkToBeSorted.slice(rr));
			}

			// if rightChunkToBeSorted poured all its elems,
			// pour in the leftChunkToBeSorted elements
			else if(rr === rightChunkToBeSorted.length){
				endResult = endResult.concat(leftChunkToBeSorted.slice(ll));
			}

			// compare the elements of both chunks
			// and pour the lowest first
			else if(leftChunkToBeSorted[ll] <= rightChunkToBeSorted[rr]){
				endResult.push(leftChunkToBeSorted[ll++]);
			}

			else {
				endResult.push(rightChunkToBeSorted[rr++]);
			}
		}

		return endResult;
	};

	//merge subarrays
	return mergeThese(sortedLeftChunk, sortedRightChunk);
};


console.log(mergeSortThis([5644,2625,1265,3864,4619,8452,8809,6789,6799,6168,3079,5724,5926,9410,7408,6079,6971,1533,4207,1498,8732,8450,8148,8761,2783,1372,2584,212,972,1689,2732,6615,4314,3996,479,8932,2448,9288,5721,9247,5455,8799,4970,1381,8208,2377,7460,5178,3910,1666,6676,2641,116,4823,1401,2898,6194,3985,3109,7166,5673,5841,3780,9987,9837,4259,8918,2284,3546,4638,1530,9001,3436,6500,381,1644,8876,7841,6822,2786,9506,3498,5426,9622,8320,6827,2519,4514,811,5627,1680,6484,1467,5459,6470,1303,9718,5388,3587,3263]));