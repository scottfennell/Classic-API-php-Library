package com.trackvia.api.client;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import com.google.api.client.auth.oauth2.AuthorizationRequestUrl;

public class RecordStrategy extends APIStrategy {
	
	private Long recordId = new Long(0);
	private String name = new String();
	private List<Map> rows = new ArrayList<Map> ();	
	
	@Override
	protected AuthorizationRequestUrl getRequestBuilder(String clientId) {
		ArrayList<String> responseTypes = new ArrayList<String> ();
		responseTypes.add("token");
		
		AuthorizationRequestUrl builder = new AuthorizationRequestUrl(super.baseUrl + "/records/" + getRecordId().toString() + ".json", clientId, responseTypes);
		builder.put("access_token", this.accessToken);
		
		return builder;
	}

	@Override
	protected void postExecute(String responseBody) {
		Map<String, Object> map = APIClient.getResponseAsMap(responseBody);
		
		setName( (String)map.get("name") );
		setRows( ((List<Map>)map.get("rows")) );

	}

	public RecordStrategy(String accessToken, Long recordId) {
		setAccessToken(accessToken);
		setRecordId(recordId);
	}

	public Long getRecordId() {
		return recordId;
	}

	private void setRecordId(Long recordId) {
		this.recordId = recordId;
	}

	public String getName() {
		return name;
	}

	private void setName(String name) {
		this.name = name;
	}
	
	public List<Map> getRows() {
		return rows;
	}

	private void setRows(List<Map> rows) {
		this.rows = rows;
	}	
}
