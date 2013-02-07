package com.trackvia.api.client;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import com.google.api.client.auth.oauth2.AuthorizationRequestUrl;

public class RecordDeleteStrategy extends APIStrategy {
	
	private String name = new String();
	private Long recordId;
	private List<Map> rows = new ArrayList<Map> ();	
	
	@Override
	protected AuthorizationRequestUrl getRequestBuilder(String clientId) {
		ArrayList<String> responseTypes = new ArrayList<String> ();
		responseTypes.add("token");
		
		AuthorizationRequestUrl builder = new AuthorizationRequestUrl(super.baseUrl + "/records/" + this.getRecordId().toString(), clientId, responseTypes);
		builder.put("access_token", this.accessToken);
		
		return builder;
	}

	@Override
	protected void postExecute(String responseBody) {
		Map<String, Object> map = APIClient.getResponseAsMap(responseBody);
		
		setName( (String)map.get("name") );
		setRows( ((List<Map>)map.get("rows")) );

	}

	public RecordDeleteStrategy(String accessToken, Long recordId, String payload) {
		setAccessToken(accessToken);
		this.setRecordId(recordId);
		super.setPayload(payload);
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
	
	private void setRecordId(Long recordId) {
		this.recordId = recordId;
	}
	
	private Long getRecordId() {
		return this.recordId;
	}
}
